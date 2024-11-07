extends Control

@onready var progressBar = $ProgressBar

func _process(_delta):
	position = Vector2(Globals.viewportRect.size.x/2-size.x, Globals.viewportRect.size.y-29)
	progressBar.value = Globals.health
