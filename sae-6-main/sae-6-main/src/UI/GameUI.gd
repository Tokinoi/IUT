extends CanvasLayer

@export var map: Node2D

@onready var MENU = preload("res://src/UI/TitleMenu.tscn")
@onready var TANK = preload("res://src/game/player/tank.tscn")
@onready var ROCK = preload("res://src/game/entity/rock.tscn")	
@onready var nameBtn = %ShowNamesBtn
@onready var BgBtn = %ShowBgBtn

func _on_spawn_dumb_btn_pressed():
	var dumbBot = TANK.instantiate()
	dumbBot.global_position = Config.spawnArea(30)
	dumbBot.rotation = randf_range(-1.0,1.0)
	map.add_child(dumbBot)

func _on_spawn_random_btn_pressed():
	var randBot = TANK.instantiate()
	randBot.global_position = Config.spawnArea(30)
	randBot.rotation = randf_range(-1.0,1.0)
	randBot.isRandom = true
	map.add_child(randBot)

func _on_show_names_btn_pressed():
	Config.areNameVisible = !Config.areNameVisible
	if Config.areNameVisible:
		nameBtn.text = "Hide Names"
	else:
		nameBtn.text = "Show Names"

func _on_reset_score_btn_pressed():
	var tanks = get_tree().get_nodes_in_group("tank")
	for tank in tanks:
		tank.score = 0

func _on_generate_map_btn_pressed():
	for child in map.get_children():
		if child is Rock:
			child.queue_free()
	for value in (float(Config.obstacleProbability)*2)*randi_range(5, 10):
		var rock = ROCK.instantiate()
		rock.global_position = Config.spawnArea(50)
		var rockScale = randf_range(1.0, 2.0)
		rock.scale = Vector2(rockScale, rockScale)
		map.add_child(rock)

func _on_show_bg_btn_pressed():
	Config.isBgVisible = !Config.isBgVisible
	if Config.isBgVisible:
		BgBtn.text = "Hide Background"
	else:
		BgBtn.text = "Show Background"

func _on_quit_game_pressed():
	get_tree().change_scene_to_file("res://src/UI/TitleMenu.tscn")
