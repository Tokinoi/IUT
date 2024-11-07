extends Node2D

var BULLET = preload("res://src/game/entity/bullet.tscn")
var cooldown = 1.0
@export var tankOwner: Tank
@export var gun_trig: bool

func _ready():
	newCooldown()

func _process(delta):
	cooldown -= delta
	
	if !gun_trig:
		pass
	
	if cooldown <= 0.0:
		var bullet = BULLET.instantiate()
		var bullet_offset = Vector2(30,0).move_toward(Vector2.from_angle(deg_to_rad(tankOwner.rotation_degrees-90)), 30.0) 
		bullet.direction_vector = Vector2.from_angle(deg_to_rad(tankOwner.rotation_degrees-90))*500
		bullet.global_position = global_position + bullet_offset
		bullet.rotation = tankOwner.rotation
		bullet.tankOwner = tankOwner
		get_tree().get_nodes_in_group("map")[0].add_child(bullet)
		newCooldown()

func newCooldown():
	cooldown = 1.0 / float(Config.rateOfFire)
