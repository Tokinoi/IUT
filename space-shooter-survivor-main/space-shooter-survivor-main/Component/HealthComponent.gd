extends Node2D
class_name HealthComponent

@export var MAX_HEALTH = 1
var health

func _ready():
	health = MAX_HEALTH

func damage(value: int):
	health -= value
	
	if health <= 0 && get_parent().has_method("onDeath"):
		get_parent().onDeath()
