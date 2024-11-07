extends DirectionalLight3D

func _on_Timer_timeout():
	if Globals.player != null:
		look_at(Globals.player.global_transform.origin, Vector3.UP)
