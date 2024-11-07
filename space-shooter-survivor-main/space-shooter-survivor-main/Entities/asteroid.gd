extends RigidBody2D

var velocity = Vector2.ZERO
var MAX_SPEED = 100
var HEALTH = 3
var realScale = Vector2(1,1)
@onready var ASTEROID = preload("res://Entities/asteroid.tscn")

func _ready():
	velocity.x = MAX_SPEED
	velocity.y = MAX_SPEED
	apply_central_impulse(velocity.rotated(deg_to_rad(randf_range(0,360))))

func _process(_delta):
	$Polygon2D.scale = realScale

func damage(value):
	HEALTH -= value
	if HEALTH <= 0:
		Globals.enemyKills += 1
		queue_free()
	else:
		addAsteroid()
		addAsteroid()
		Globals.enemyKills += 1
		queue_free()

func addAsteroid():
	var asteroid = ASTEROID.instantiate()
	asteroid.global_position = global_position
	asteroid.HEALTH = HEALTH-1
	asteroid.realScale = scale/2
	get_parent().call_deferred('add_child',asteroid)
