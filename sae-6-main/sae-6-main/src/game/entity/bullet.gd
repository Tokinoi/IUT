extends Area2D

var tankOwner: Tank
var direction_vector : Vector2

func _process(delta):
	translate(direction_vector*delta)

func _on_timer_timeout():
	queue_free()

func _on_body_entered(body):
	if body != tankOwner:
		if body is Tank:
			body.hit(tankOwner)
		queue_free()

func stream():
	return str(self.rotation_degrees) +'!'+ str(self.position)
