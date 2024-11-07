extends CanvasLayer

@onready var EXP_PANEL = preload("res://GUI/expBar.tscn")
@onready var HEALTH_BAR = preload("res://GUI/healthBar.tscn")
@onready var BOOST_BAR = preload("res://GUI/boostBar.tscn")
@onready var PAUSE_MENU = preload("res://Menu/pause_menu.tscn")
@onready var DEATH_MENU = preload("res://Menu/DeathMenu.tscn")
var expPanel
var healthBar
var boostBar
var pauseMenu
var deathTitle

func _ready():
	expPanel = EXP_PANEL.instantiate()
	add_child(expPanel)
	healthBar = HEALTH_BAR.instantiate()
	add_child(healthBar)
	boostBar = BOOST_BAR.instantiate()
	add_child(boostBar)
	
	pauseMenu = PAUSE_MENU.instantiate()
	pauseMenu.visible = false
	add_child(pauseMenu)
	deathTitle = DEATH_MENU.instantiate()
	deathTitle.visible = false
	add_child(deathTitle)

func _process(_delta):
	Globals.viewportRect = owner.get_viewport_rect()

func gameEnded():
	expPanel.visible = false
	healthBar.visible = false
	pauseMenu.visible = false
	boostBar.visible = false
	deathTitle.visible = true
	
	
func toggleMenu():
	pauseMenu.visible = !pauseMenu.visible
