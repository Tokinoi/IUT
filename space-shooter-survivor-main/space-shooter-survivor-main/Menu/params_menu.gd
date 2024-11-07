extends Control

@onready var GAME_TITLE = preload("res://Menu/game_title.tscn")
@onready var menu = $"../mainMenu"
var gameTitle

func _ready():
	gameTitle = GAME_TITLE.instantiate()
	add_child(gameTitle)

func _process(_delta):
	gameTitle.position = Vector2(get_viewport_rect().size.x/2-gameTitle.size.x/2, 150)

func _on_franais_pressed():
	TranslationServer.set_locale("fr")

func _on_english_pressed():
	TranslationServer.set_locale("en")

func _on_exit_params_pressed():
	self.visible = false
	menu.visible = true
