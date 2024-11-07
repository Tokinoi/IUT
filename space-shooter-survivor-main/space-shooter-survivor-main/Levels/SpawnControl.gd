extends Node2D

@onready var ENEMY = preload("res://TEST/enemyTest.tscn")
@onready var ASTEROID = preload("res://Entities/asteroid.tscn")
@onready var player = get_tree().get_nodes_in_group("player")[0]

@export_range(1,1000) var cycleChance = 50

var timePassed = 0
var spawnPos
	
func _process(delta):
	player = get_tree().get_nodes_in_group("player")[0]
	timePassed += delta
	if randi_range(1,1000) > 1000-cycleChance-int(timePassed):
		cycle()

func cycle():
	randSpawn()
	var newEnemy = ENEMY.instantiate()
	if is_instance_valid(player):
		newEnemy.position = player.position + spawnPos
	newEnemy.scale = Vector2(2, 2)
	if randi_range(0,10) == 0:
		newEnemy = champion(newEnemy)
	add_child(newEnemy)
	
	if randi_range(1,10) == 1:
		randSpawn()
		var asteroid = ASTEROID.instantiate()
		if is_instance_valid(player):
			asteroid.position = player.position + spawnPos
		add_child(asteroid)

func champion(newEnemy):
	var risk = randi_range(3,10)
	newEnemy.scale = Vector2(2+risk*0.3, 2+risk*0.3)
	newEnemy.modulate = Color(randf_range(0.4,1), randf_range(0.4,1), randf_range(0.4,1))
	newEnemy.HEALTH = risk
	newEnemy.EXP_VALUE = risk*1.2
	newEnemy.MAX_SPEED = 100.0 - risk*1.5
	return newEnemy

func randSpawn():
	var screenSize = get_viewport_rect().size
	var spawnPosX
	var spawnPosY
	if randi_range(0,1):
		spawnPosX = randi_range(-screenSize.x/2-50, screenSize.x/2+50)
		spawnPosY = screenSize.y/2+50 if randi_range(0,1) else -screenSize.y/2-50
	else:
		spawnPosX = screenSize.x/2+50 if randi_range(0,1) else -screenSize.x/2-50
		spawnPosY = randi_range(-screenSize.y/2-50, screenSize.y/2+50)
	spawnPos = Vector2(spawnPosX, spawnPosY)
