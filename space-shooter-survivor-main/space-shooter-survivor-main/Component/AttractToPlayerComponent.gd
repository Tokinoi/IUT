extends Node2D
class_name AttractToPlayerComponent

var velocity: Vector2
@export var effectiveRange = 100

func _process(delta):
	if Globals.player != null:
		if global_position.distance_to(Globals.player.position) < effectiveRange:
			look_at(Globals.player.position)
			velocity.x = cos(rotation_degrees * PI / 180) * delta * 100
			velocity.y = sin(rotation_degrees * PI / 180) * delta * 100
			owner.translate(velocity)
