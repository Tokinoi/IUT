extends Node2D

var shrinkTime = 5.0
var shrinkCooldown = 10.0

func _ready():
	global_position += Vector2(randi_range(-200,200), randi_range(-100,100))

func _process(delta):
	shrinkCooldown -= delta
	if shrinkCooldown <= 0:
		shrinkTime -= delta
		scale = scale * 0.999
		Config.shrinking = true
	if shrinkTime <= 0:
		Config.shrinking = false
		shrinkCooldown = 10.0
		shrinkTime = 5.0
	
	Config.stormPos = global_position
