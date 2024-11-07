extends Label

func _process(_delta):
	global_position.x = int(Config.arenaWidth)/2-150
	global_position.y = int(Config.arenaHeight)-40
	visible = Config.shrinking
