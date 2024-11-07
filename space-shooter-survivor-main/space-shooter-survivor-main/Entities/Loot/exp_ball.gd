extends Area2D

@export_range(1,100) var VALUE = 1

func _on_body_entered(body):
	if body != null:
		if body.is_in_group("player"):
			body.CURRENT_EXP += VALUE
			queue_free()
