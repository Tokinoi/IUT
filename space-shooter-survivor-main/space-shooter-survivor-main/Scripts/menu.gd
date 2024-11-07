extends Control

@onready var transition = $Transition
@onready var GAME_TITLE = preload("res://Menu/game_title.tscn")
@onready var camera3D2 = $"../Camera3D2"
@onready var planet_menu = $"../planetMenu"
@onready var LineMe = $"../SpatialMe/LineMe"
@onready var LineVe = $"../SpatialVe/LineVe"
@onready var LineEa = $"../SpatialEa/LineEa"
@onready var LineMa = $"../SpatialMa/LineMa"
@onready var LineJu = $"../SpatialJu/LineJu"
@onready var LineSa = $"../SpatialSa/LineSa"
@onready var LineUr = $"../SpatialUr/LineUr"
@onready var LineNe = $"../SpatialNe/LineNe"
@onready var params_menu = $"../ParamsMenu"
@onready var Buttons = $MarginContainer
var gameTitle

func _ready():
	hideLines()
	planet_menu.visible = false
	gameTitle = GAME_TITLE.instantiate()
	add_child(gameTitle)

func _process(_delta):
	gameTitle.position = Vector2(get_viewport_rect().size.x/2-gameTitle.size.x/2, 150)

func _on_quit_pressed():
	get_tree().quit()

func _on_option_pressed():
	self.visible = false
	params_menu.visible = true
	
func _on_play_pressed():
	transition.play("fade_out")
	await get_tree().create_timer(1).timeout
	showLines()
	camera3D2.current = true
	self.visible = false
	Globals.gameStyle = 1
	planet_menu.visible = true
	transition.play("fade_in")

func _on_play_2_pressed():
	transition.play("fade_out")
	await get_tree().create_timer(1).timeout
	showLines()
	camera3D2.current = true
	self.visible = false
	Globals.gameStyle = 2
	planet_menu.visible = true
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

