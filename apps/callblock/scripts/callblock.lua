--
--      CallBlock-FS
--      Version: MPL 1.1
--
--      The contents of this file are subject to the Mozilla Public License Version
--      1.1 (the "License"); you may not use this file except in compliance with
--      the License. You may obtain a copy of the License at
--      http://www.mozilla.org/MPL/
--
--      Software distributed under the License is distributed on an "AS IS" basis,
--      WITHOUT WARRANTY OF ANY KIND, either express or implied. See the License
--      for the specific language governing rights and limitations under the
--      License.
--
--      The Original Code is CallBlock-FS
--
--      The Initial Developer of the Original Code is
--      Gerrit Visser <gerrit308@gmail.com>
--      Copyright (C) 2011
--      the Initial Developer. All Rights Reserved.
--
--      Contributor(s):
--      Gerrit Visser <gerrit308@gmail.com>
--[[
This module provides for Blacklisting of phone numbers. Essentially these are numbers that you do not want to hear from again!

To call this script and pass it arguments:
1. On the command line, e.g. in a FS incoming dialplan: <action application="lua" data="callblock.lua C"/>
This method causes the script to get its manadatory arguments directly from the Session
]]
--[[ Change Log:
	15 Jun, 2011: initial release > FusionPBX
	15 Jun, 2011: Added loglevel parameter and logger function to simplify control of debug output
	4 May, 2012: tested with FusionPBX V3
	4 May, 2012: added per_tenant capability (domain based)
]]
-- Data
	-- Command line parameters
local	params = {	cmd="",
					cid_num = tostring(session:getVariable("caller_id_number")),
					cid_name = session:getVariable("caller_id_name"),
					domain = session:getVariable("domain"),
					userid= "", -- session:getVariable("id")
					loglevel = "W" -- Warning, Debug, Info
					}

	-- local storage
local sql = nil

local function logger(level, log, data)
-- output data to console 'log' if debug level is on
	if string.find(params["loglevel"], level) then
		freeswitch.consoleLog(log, "CallBlock: " .. data .. "\n")
	end
end

--include the lua script
	scripts_dir = string.sub(debug.getinfo(1).source,2,string.len(debug.getinfo(1).source)-(string.len(argv[0])+1));
	include = assert(loadfile(scripts_dir .. "/resources/config.lua"));
	include();

--connect to the database
--ODBC - data source name
	if (dsn_name) then
		dbh = freeswitch.Dbh(dsn_name,dsn_username,dsn_password);
	end
--FreeSWITCH core db handler
	if (db_type == "sqlite") then
		dbh = freeswitch.Dbh("core:"..db_path.."/"..db_name);
	end

if dbh:connected() == false then
	logger("W", "NOTICE", "db was not connected")
end

	-- We have a single command letter
	-- Use session variables
	logger("D", "NOTICE", "params default from session, count " .. string.format("%d", #argv[1]) .. " \n")
	params["cmd"] = argv[1]

session:setVariable("callblock", "")	-- ensure that we have a fresh status on exit

logger("D", "NOTICE", "params are: " .. string.format("'%s', '%s', '%s', '%s'", params["cid_num"], 
		params["cid_name"], params["userid"], params["domain"]))

--Check if number is in callblock list
--	If it is, then increment the counter and block the call
if (params["cmd"] == "C") then
	sql = "SELECT * FROM v_callblock as c "
	sql = sql .. "JOIN v_domains as d ON c.domain_uuid=d.domain_uuid "
	sql = sql .. "WHERE c.blocked_caller_number = '" .. params["cid_num"] .. "' AND d.domain_name = '" .. params["domain"] .."'"
	status = dbh:query(sql, function(rows)
		found_cid_num = rows["blocked_caller_number"]
		found_uuid = rows["blocked_caller_uuid"]
		found_enabled = rows["block_call_enabled"]
		found_action = rows["blocked_call_action"]
		found_count = rows["blocked_call_count"]
		end)
	-- dbh:affected_rows() doesn't do anything if using core:db so this is the workaround:
	if found_cid_num then	-- caller id exists
		if (found_enabled == "true") then
			details = {}
			k = 0
			for v in string.gmatch(found_action, "%w+") do
				details[k] = v
				--logger("W", "INFO", "Details: " .. details[k])
				k = k + 1
			end
			dbh:query("UPDATE v_callblock SET blocked_call_count = " .. found_count + 1 .. " WHERE blocked_caller_uuid = '" .. found_uuid .. "'")
			session:setVariable("callblock", "block")
			logger("W", "NOTICE", "number " .. params["cid_num"] .. " blocked with " .. found_count .. " previous hits, domain: " .. params["domain"])
			if (found_action == "Reject") then 	
				session:hangup("CALL_REJECTED")
			end
			if (found_action == "Busy") then 	
				session:hangup("USER_BUSY")
			end
			if (details[0] =="Voicemail") then
				session:setAutoHangup(false)
				session:execute("voicemail", details[1] .. " " .. session:getVariable("domain") .. " " .. details[2])
			end
		end
	end
end
dbh:release()
