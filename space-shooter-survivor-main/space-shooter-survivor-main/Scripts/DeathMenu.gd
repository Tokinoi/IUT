extends Control

func _on_quit_pressed():
	get_tree().quit()	

func _on_return_pressed():
	get_tree().change_scene_to_file("res://Menu/solar_system.tscn")

func _on_reload_pressed():
	get_tree().reload_current_scene()
