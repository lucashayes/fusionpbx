-- fs_bind.lua
-- Written by E. Schmidbauer <e.schmidbauer@gmail.com>

-- comment the following line for production:
--freeswitch.consoleLog("notice", "fission.lua experiment provided params:\n" .. params:serialize() .. "\n")
local req_domain = params:getHeader("domain")
local req_key    = params:getHeader("key")
local req_user   = params:getHeader("user")
local req_context= params:getHeader("variable_user_context")
local req_dnumber= params:getHeader("Caller-Destination-Number")
local req_cidnum = params:getHeader("Caller-Caller-ID-Number")
--freeswitch.consoleLog("notice", "section " .. XML_REQUEST["section"] .. "\n")
--freeswitch.consoleLog("notice", "tag_name " .. XML_REQUEST["tag_name"] .. "\n")
--freeswitch.consoleLog("notice", "key_name " .. XML_REQUEST["key_name"] .. "\n")
--freeswitch.consoleLog("notice", "key_value " .. XML_REQUEST["key_value"] .. "\n")

local dbh = freeswitch.Dbh("fusionpbx_odbc","root","root_password")
if dbh:connected() == false then
  freeswitch.consoleLog("notice", "fission.lua cannot connect to database" .. dsn .. "\n")
  return
end

if ((XML_REQUEST["section"] == "directory") and (req_domain) and (req_user)) then
	-- it's probably wise to sanitize input to avoid SQL injections !
	local my_query = string.format("select (select v_domain from v_system_settings where v_system_settings.v_id=v_extensions.v_id) as domain, user_context, extension, number_alias, password, vm_password, vm_enabled, vm_mailto, vm_attach_file, vm_keep_local_after_email, toll_allow, accountcode, limit_max, outbound_caller_id_number, outbound_caller_id_name, effective_caller_id_number, effective_caller_id_name from v_extensions where v_id in (select v_id from v_system_settings where v_domain='%s') and extension='%s'", req_domain, req_user)

	assert (dbh:query(my_query, function(u) -- there will be only 0 or 1 iteration (limit 1)
	  XML_STRING =
	[[<?xml version="1.0" encoding="UTF-8" standalone="no"?>
	<document type="freeswitch/xml">
	  <section name="directory">
	    <domain name="]] .. u.domain .. [[">
	      <user id="]] .. u.extension .. [[">
	        <params>
	          <param name="password" value="]] .. u.password .. [["/>
	          <param name="vm-password" value="]] .. u.vm_password  .. [["/>
	          <param name="vm-enabled" value="]] .. u.vm_enabled .. [["/>
	          <param name="vm-email-all-messages" value="]] .. u.vm_enabled  ..[["/>
	          <param name="vm-attach-file" value="]] .. u.vm_attach_file .. [["/>
	          <param name="vm-keep-local-after-email" value="]] .. u.vm_keep_local_after_email .. [["/>
	          <param name="vm-mailto" value="]] .. u.vm_mailto .. [["/>
	          <param name="dial-string" value="{sip_invite_domain=${domain_name},presence_id=${dialed_user}@${dialed_domain}}${sofia_contact(${dialed_user}@${dialed_domain})}"/>
	        </params>
	        <variables>
		  <variable name="effective_caller_id_number" value="]] .. u.effective_caller_id_number.. [["/>
		  <variable name="outbound_caller_id_number" value="]] .. u.outbound_caller_id_number .. [["/>
	          <variable name="toll_allow" value="]] .. u.toll_allow .. [["/>
	          <variable name="accountcode" value="]] .. u.accountcode .. [["/>
	          <variable name="user_context" value="]] .. u.user_context .. [["/>
	          <variable name="limit_max" value="]] .. u.limit_max .. [["/>
	          <variable name="record_stereo" value="true"/>
	          <variable name="default_gateway" value="$${default_provider}"/>
	          <variable name="default_areacode" value="$${default_areacode}"/>
	          <variable name="transfer_fallback_extension" value="operator"/>
	          <variable name="export_vars" value="domain_name"/>
	        </variables>
	      </user>
	    </domain>
	  </section>
	</document>]]
	-- comment the following line for production:
	-- freeswitch.consoleLog("notice", "Debug from fission.lua, generated XML:\n" .. XML_STRING .. "\n")
	end))
end

if ((XML_REQUEST["section"] == "dialplan") and (req_context)) then 
	-- freeswitch.consoleLog("notice", "Debug in dialplan section! Called number: " .. req_dnumber .. "\n")
	-- freeswitch.consoleLog("notice", "Debug in dialplan section! Caller's number: " .. req_cidnum .. "\n")
	freeswitch.consoleLog("notice", "fission.lua experiment provided params:\n" .. params:serialize() .. "\n")

	local my_query 
	local v_id
	local lua_action_id
	local caller_route_id
	local time_route_id
	local destination_type
	local route_debug
	local ext_xml = [[<extension name="lua_generated">]]	
	
	my_query = string.format("select v_id, lua_action_id, caller_route_id, time_route_id, destination_type from v_dialplan_lua_routes where enabled='true' and v_domain='%s' and dialed_number='%s' order by lua_order asc", req_context, req_dnumber)
	assert (dbh:query(my_query, function(u)
		v_id = u.v_id
		lua_action_id = u.lua_action_id
		caller_route_id = u.caller_route_id
		time_route_id = u.time_route_id
		destination_type = u.destination_type
		route_debug = "Normal Route Taken. "
	
		if(lua_time_group) then
			my_query = "select lua_action_id from v_dialplan_lua_time_routes where v_id='" .. v_id .. "' and time_group='" .. lua_time_group .. "' and ( start_hour<hour(now()) or ( start_hour=hour(now()) and start_minute<minute(now()) ) or ( start_hour=hour(now()) and start_minute=minute(now()) and start_second<=second(now()) ) ) and ( end_hour>hour(now()) or ( end_hour=hour(now()) and end_minute>minute(now()) ) or ( end_hour=hour(now()) and end_minute=minute(now()) and end_second>=second(now()) ) ) and days_week like concat('%%,', dayofweek(now()), ',%%') and days_month like concat('%%,', dayofmonth(now()), ',%%') and months like concat('%%,', month(now()), ',%%') and years like concat('%%,', year(now()), ',%%') order by lua_order asc limit 1"
			-- freeswitch.consoleLog("notice", "Debug in dialplan section!!!! Time SQL: " .. my_query .. "\n")
			assert (dbh:query(my_query, function(u)
				lua_action_id = u.lua_action_id
				route_debug = "Time Route Taken. "
			end))
		end		
		if (caller_route_id) then
			my_query = string.format("select lua_action_id from v_dialplan_lua_caller_routes where enabled='true' and v_id='%s' and caller_route_id='%s' and '%s' like concat(cid_prefix, '%%') order by lua_order asc limit 1", v_id, caller_route_id, req_cidnum)
			-- freeswitch.consoleLog("notice", "Debug in dialplan section!!!! Time SQL: " .. my_query .. "\n")
			assert (dbh:query(my_query, function(u)
				lua_action_id = u.lua_action_id
				route_debug = "Caller Route Taken. "
			end))
		end
		
		freeswitch.consoleLog("notice", "Debug in dialplan section!!!! " .. route_debug .." Final Action: " .. lua_action_id .. "\n")
		
		if (destination_type == "callcenter") then
			-- need to remove the caller id prefix for call center so that it can be re-attached with an updated one
			ext_xml = ext_xml .. [[<condition field="${caller_id_name}" expression="^([^#]+#)(.*)$" break="never" ><action application="set" data="caller_id_name=$2" /></condition>]]
		end
		
		ext_xml = ext_xml .. [[<condition>]]
		my_query = string.format("select application, data from v_dialplan_lua_actions where v_id='%s' and lua_action_id='%s' order by lua_order", v_id, lua_action_id)
		assert (dbh:query(my_query, function(u)
			ext_xml = ext_xml .. [[<action application="]] .. u.application .. [["]]
			if (string.len(u.data) == 0) then
				ext_xml = ext_xml .. [[ />]]
			else
				ext_xml = ext_xml .. [[ data="]] .. u.data .. [[" />]]
			end
		end))
		
		ext_xml = ext_xml .. [[</condition></extension>]]
		
		freeswitch.consoleLog("notice", "Debug: " .. ext_xml .. "\n")
		
	end))
	XML_STRING = [[
		<?xml version="1.0" encoding="UTF-8" standalone="no"?>
		<document type="freeswitch/xml">
		  <section name="dialplan" description="FissionPBX">
			<context name="]] .. req_context .. [[">
			  ]] .. ext_xml .. [[		
			</context>
		  </section>
		</document>]]
end

