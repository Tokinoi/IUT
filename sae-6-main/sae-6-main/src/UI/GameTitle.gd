extends Control


func _on_start_btn_pressed():
	get_tree().change_scene_to_file("res://src/game/maps/map.tscn")

func _on_battle_royale_pressed():
	get_tree().change_scene_to_file("res://src/game/maps/battleRoyaleMap.tscn")

func _on_settings_btn_pressed():
	get_tree().change_scene_to_file("res://src/UI/GameSettings.tscn")

func _on_quit_btn_pressed():
	get_tree().quit()
