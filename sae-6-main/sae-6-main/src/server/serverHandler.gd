extends Node2D

@onready var TANK = preload("res://src/game/player/tank.tscn")
var server : TCPServer
var clients
var stream_server : TCPServer

func _ready():
	clients = []
	server = TCPServer.new()
	var port: int = 4242
	print(server.listen(port))
	print("Server started on port %d" % port)
	stream_server = TCPServer.new()
	print(server.listen(6969))

func _process(delta):
	if server.is_connection_available():
		var client: StreamPeerTCP = server.take_connection()
		print(client)
		if client:
			print("Client connected")
			var tank = TANK.instantiate()
			tank.set_controller(TCPControllerAdapter.new(client))
			
			add_child(tank)
	if stream_server.is_connection_available():
		var client: StreamPeerTCP = server.take_connection()
		print(client)
		if client:
			print("Client connected")
			clients.append(client)
	stream()
	
func stream():
	var message = 'TANK='
	var group_members = get_tree().get_nodes_in_group("tank")
	for tank in group_members:
		message += tank.stream() + ';'
	
	message += '#Bullet='
	group_members = get_tree().get_nodes_in_group("bullet")
	for bullet in group_members:
		message += bullet.stream() + ';'
	
	message += '#Rock='
	group_members = get_tree().get_nodes_in_group("rock")
	for rock in group_members:
		message += rock.stream() + ';'
	
	for client in clients: 
		client.put_data(message.to_utf8_buffer())
		
