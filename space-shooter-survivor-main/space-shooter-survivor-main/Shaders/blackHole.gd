extends Node2D

var velocity: Vector2

func _process(delta):
	if is_instance_valid(Globals.player):
		look_at(Globals.player.position)
		velocity.x = cos(rotation_degrees * PI / 180) * delta * 100
		velocity.y = sin(rotation_degrees * PI / 180) * delta * 100
		translate(velocity)

func _on_inner_area_2d_area_entered(area):
	kill(area)

func _on_inner_area_2d_body_entered(body):
	kill(body)

func kill(body):
	if body is Enemy:
		body.queue_free()
	if body.has_method("damage"):
		body.damage(99)
