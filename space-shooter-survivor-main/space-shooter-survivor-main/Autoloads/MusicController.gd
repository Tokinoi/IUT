extends Node2D

@onready var aloneAgainstEnemy = $AloneAgainstEnemy
@onready var epicEnd = $EpicEnd
@onready var rainOfLaser = $RainOfLaser
@onready var withoutFear = $WithoutFear

func _ready():
	newMusic()

func newMusic():
	match randi_range(1,4):
		1:
			aloneAgainstEnemy.play()
		2:
			epicEnd.play()
		3:
			rainOfLaser.play()
		4:
			withoutFear.play()


func _on_alone_against_enemy_finished():
	newMusic()

func _on_epic_end_finished():
	newMusic()

func _on_rain_of_laser_finished():
	newMusic()

func _on_without_fear_finished():
	newMusic()
