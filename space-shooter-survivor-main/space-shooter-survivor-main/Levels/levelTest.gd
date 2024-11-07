extends Node2D

@onready var canvasLayer = $CanvasLayer
@onready var atmosTimer = $AtmosTimer
@onready var spaceBackground = $SpaceBackground
var player
var camera
@onready var ATMOSPHERE = preload("res://Particles/atmosphereParticles.tscn")
var atmos1
var atmos2
var atmos2SpawnPos
@onready var atmos3 = ATMOSPHERE.instantiate()
@onready var atmos3SpawnPos = atmos3.position
var switchAtmos = false

func _ready():
	if Globals.gameStyle == 1:
		$PlayerRoid.queue_free()
		player = $Player
	else:
		$Player.queue_free()
		player = $PlayerRoid
	camera = player.get_node("Camera2D")
	
	atmos1 = ATMOSPHERE.instantiate()
	atmos1.position = player.global_position
	atmos1.amount = 500
	atmos1.z_index = -1
	atmos1.scale = Vector2(0.8, 0.8)
	add_child(atmos1)
	atmos2 = ATMOSPHERE.instantiate()
	atmos2.position = player.global_position
	atmos2SpawnPos = atmos2.position
	atmos2.one_shot = true
	atmos2.amount = 1000
	atmos2.z_index = -2
	atmos2.local_coords = true
	add_child(atmos2)

func _process(_delta):
	Globals.player = player
	spaceBackground.position = camera.global_position - camera.offset
	atmos1.position = player.global_position
	atmos2.position = atmos2SpawnPos - (atmos2SpawnPos - player.global_position) * 0.9
	atmos3.position = atmos3SpawnPos - (atmos3SpawnPos - player.global_position) * 0.9
	
	if Input.is_action_just_pressed("Pause"):
		get_tree().paused = !get_tree().paused
		canvasLayer.toggleMenu()

func levelEnded():
	canvasLayer.gameEnded()

func _on_atmos_timer_timeout():
	if switchAtmos:
		atmos2 = ATMOSPHERE.instantiate()
		atmos2.position = player.global_position
		atmos2SpawnPos = atmos2.position
		atmos2.one_shot = true
		atmos2.amount = 1000
		atmos2.z_index = -2
		atmos2.local_coords = true
		add_child(atmos2)
		switchAtmos = false
	else:
		atmos3 = ATMOSPHERE.instantiate()
		atmos3.position = player.global_position
		atmos3SpawnPos = atmos3.position
		atmos3.one_shot = true
		atmos3.amount = 1000
		atmos3.z_index = -2
		atmos3.local_coords = true
		add_child(atmos3)
		switchAtmos = true
