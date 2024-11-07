extends Control

@onready var transition = $Transition
@onready var menu = $"../mainMenu"
@onready var camera3D = $"../Camera3D"
@onready var camera3D2 = $"../Camera3D2"
@onready var cameraMe = $"../SpatialMe/CameraMe"
@onready var cameraVe = $"../SpatialVe/CameraVe"
@onready var cameraEa = $"../SpatialEa/CameraEa"
@onready var cameraMa = $"../SpatialMa/CameraMa"
@onready var cameraJu = $"../SpatialJu/CameraJu"
@onready var cameraSa = $"../SpatialSa/CameraSa"
@onready var cameraUr = $"../SpatialUr/CameraUr"
@onready var cameraNe = $"../SpatialNe/CameraNe"
@onready var LineMe = $"../SpatialMe/LineMe"
@onready var LineVe = $"../SpatialVe/LineVe"
@onready var LineEa = $"../SpatialEa/LineEa"
@onready var LineMa = $"../SpatialMa/LineMa"
@onready var LineJu = $"../SpatialJu/LineJu"
@onready var LineSa = $"../SpatialSa/LineSa"
@onready var LineUr = $"../SpatialUr/LineUr"
@onready var LineNe = $"../SpatialNe/LineNe"
@onready var LabelMe = $LabelMe
@onready var LabelVe = $LabelVe
@onready var LabelEa = $LabelEa
@onready var LabelMa = $LabelMa
@onready var LabelJu = $LabelJu
@onready var LabelSa = $LabelSa
@onready var LabelUr = $LabelUr
@onready var LabelNe = $LabelNe
@onready var Start = $MarginContainer3/VBoxContainer/Start


func _on_mercury_pressed():
	focusPlanet(cameraMe)
	hideDesc()
	LabelMe.visible = true

func _on_venus_pressed():
	focusPlanet(cameraVe)
	hideDesc()
	LabelVe.visible = true

func _on_earth_pressed():
	focusPlanet(cameraEa)
	hideDesc()
	LabelEa.visible = true

func _on_mars_pressed():
	focusPlanet(cameraMa)
	hideDesc()
	LabelMa.visible = true

func _on_jupiter_pressed():
	focusPlanet(cameraJu)
	hideDesc()
	LabelJu.visible = true

func _on_saturn_pressed():
	focusPlanet(cameraSa)
	hideDesc()
	LabelSa.visible = true

func _on_uranus_pressed():
	focusPlanet(cameraUr)
	hideDesc()
	LabelUr.visible = true

func _on_neptune_pressed():
	focusPlanet(cameraNe)
	hideDesc()
	LabelNe.visible = true

func _on_solar_pressed():
	showLines()
	hideDesc()
	camera3D2.current = true
	Start.visible = false

func _on_start_pressed():
	get_tree().change_scene_to_file("res://Levels/level_test.tscn")

func _on_return_pressed():
	transition.play("fade_out")
	await get_tree().create_timer(1).timeout
	hideLines()
	camera3D.current = true
	self.visible = false
	menu.visible = true
	transition.play("fade_in")

func showLines():
	LineMe.visible = true
	LineVe.visible = true
	LineEa.visible = true
	LineMa.visible = true
	LineJu.visible = true
	LineSa.visible = true
	LineUr.visible = true
	LineNe.visible = true

func hideLines():
	LineMe.visible = false
	LineVe.visible = false
	LineEa.visible = false
	LineMa.visible = false
	LineJu.visible = false
	LineSa.visible = false
	LineUr.visible = false
	LineNe.visible = false

func focusPlanet(nextCamera):
	hideLines()
	nextCamera.current = true
	Start.visible = true

func hideDesc():
	LabelMe.visible = false
	LabelVe.visible = false
	LabelEa.visible = false
	LabelMa.visible = false
	LabelJu.visible = false
	LabelSa.visible = false
	LabelUr.visible = false
	LabelNe.visible = false	
