extends RigidBody2D

@export var power = 0.0

func _process(delta):
	var thrust = Vector2.from_angle(deg_to_rad(rotation_degrees-90))
	apply_central_force(global_transform.basis_xform(thrust * power * 100000))
