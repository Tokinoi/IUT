extends Button


# Called when the node enters the scene tree for the first time.
func _ready():
	pass # Replace with function body.


# Called every frame. 'delta' is the elapsed time since the previous frame.
func _process(delta):
	pass


func _on_pressed():
	#Connect to Server 
	# Wait until connection 
	var connection = StreamPeerTCP.new()
	connection.connect_to_host('192.168.178.2',6969)
	while connection.get_status() != connection.STATUS_CONNECTED:
		connection.poll()
	var scene_preload = load("res://src/game_screen.tscn")
	var new_scene = scene_preload.instantiate()
	new_scene.playerControll = connection
	get_tree().root.add_child(new_scene)
	get_tree().root.get_child(0).queue_free()
