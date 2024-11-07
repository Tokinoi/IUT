extends Node2D

@onready var gameUI	 = $GameUI

func _process(_delta):
	if Input.is_action_just_pressed("Menu"):
		gameUI.visible = not gameUI.visible
	$TileMap.visible = Config.isBgVisible
 
