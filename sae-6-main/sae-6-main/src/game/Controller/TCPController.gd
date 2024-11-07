class_name TCPControllerAdapter extends Controller

var streamTCP: StreamPeerTCP

func col(red_value: int, blue_value: int = -1, green_value: int = -1):
	if blue_value < 0:
		return super.col(red_value)
	else:
		super.col_separed(red_value, blue_value, green_value)


func nlist() -> String:
	return super.nlist()


func cbot(pos: Vector2) -> String:
	return super.cbot(pos)


func nbot(enemy) -> String:
	return super.nbot(enemy)

func cproj() -> Vector2:
	var proj: Vector2 = super.cproj()
	# return str(proj.x) + "/" + str(proj.y)
	return proj

func motr(power: float):
	print("le parse marche")
	super.motr(power)

func _init(client: StreamPeerTCP = null):
	streamTCP = client


func process():
	streamTCP.poll()
	if streamTCP.get_status() == 2:
		var data: int = streamTCP.get_available_bytes()

		if data > 0:
			var received_data: String = get_trimmed_data(streamTCP.get_string(data))
			
			if received_data != "":
				var info_to_send = PackedStringArray()
				var commands_string = get_commands_string(received_data)
				
				for command_string in commands_string:
					if command_string == "":
						pass

					var command_name: String = get_command_name(command_string)
					var command_args: PackedStringArray = get_command_args(command_string)
					var command_args_array: Array = Array(command_args)
					command_args_array.map(func(item): return float(item))

					var res = call_command(command_name, command_args_array)
					print(res)
					
					if res != null:
						info_to_send.append(to_formated_string(res))

				if info_to_send.size() != 0:
					var test = "#".join(info_to_send)
					streamTCP.put_data(test.to_utf8_buffer())

	elif (streamTCP.get_status() == 3) or streamTCP.get_status() == 0:
		print("Client disconnected, code=", streamTCP.get_status())
		tank.queue_free()

func to_formated_string(value):
	if value is Vector2:
		return str(value.x) + "/" + str(value.y)
	elif value is int:
		return str(value)
	elif value is float:
		return str(snappedf(value, 0.1)).replace(".0", "")
	elif value is String:
		return value
	elif value is Array:
		return "=".join(value)
	else: # SHOULD NEVER HAPPEN UNTIL WE ADD NEW RETURN TYPE FOR COMMANDS
		return str(value)


func get_trimmed_data(old: String) -> String:
	var new = ""
	while true:
		new = old.trim_suffix("\n").trim_suffix("#")
		if old == new:
			break
		old = new
	return new

func get_commands_string(data: String) -> PackedStringArray:
	return data.replace("\n", "#").split("#", false)


func get_command_name(data: String) -> String:
	return data.split("=")[0].to_lower()


func get_command_args(data: String) -> PackedStringArray:
	var splitted_message: PackedStringArray = data.split("=")
	splitted_message.remove_at(0)
	return splitted_message

func call_command(command_name: String, args: Array):
	match command_name:
		"name":
			return name(args[0])
		"col":
			return col(int(args[0]), int(args[1]), int(args[2]))
		"exit":
			return exit()
		"live":
			return live()
		"msg":
			return msg(args[0])
		"nlist":
			return nlist()
		"usrmsg":
			return usrmsg(args[0])
		"orient":
			return orient()
		"cbot":
			return cbot(tank.position)
		"nbot":
			return nbot(args[0])
		"cproj":
			return cproj()
		"motr":
			return motr(float(args[0]))
		"motl":
			return motl(float(args[0]))
		"guntrig":
			return guntrig(float(args[0]))
		"guntrav":
			return guntrav(float(args[0]))
		
