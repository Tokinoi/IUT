extends Node2D

@onready var gameUI	 = $GameUI

func _process(_delta):
	if Input.is_action_just_pressed("Menu"):
		gameUI.visible = not gameUI.visible
	$TileMap.visible = Config.isBgVisible
	
	var tanks = get_tree().get_nodes_in_group('tank')
	var inAreaTanks = %StormArea.get_overlapping_bodies()
	for tank in tanks:
		if tank not in inAreaTanks && tank.hasProcessStarted:
			tank.hit()
