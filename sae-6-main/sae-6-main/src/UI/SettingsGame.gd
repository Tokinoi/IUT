extends Control

# Fast loading /!\ DO NOT DELETE
@onready var tiltedMenu = preload("res://src/UI/TitleMenu.tscn")

func _on_quit_btn_pressed():
	get_tree().change_scene_to_file("res://src/UI/TitleMenu.tscn")
