extends Node

# General
var camera
var viewportRect
var is_paused
var fullscreen = true
var level
var gameStyle

# Menu
var celestial_bodies = []
var gravitational_constant = 0.000000000674

# Player
var player
var health
var boost_timer
var enemyKills = 0

func _ready():
	DisplayServer.window_set_mode(DisplayServer.WINDOW_MODE_FULLSCREEN)

func _process(_delta):
	if Input.is_action_pressed("F"):
		if fullscreen:
			DisplayServer.window_set_mode(DisplayServer.WINDOW_MODE_WINDOWED)
			fullscreen = false
		else:
			DisplayServer.window_set_mode(DisplayServer.WINDOW_MODE_FULLSCREEN)
			fullscreen = true
