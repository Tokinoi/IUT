extends Control

@onready var progressBar = $ProgressBar

func _process(_delta):
	var boostTimerRatio = clamp(Globals.boost_timer / 5, 0.0, 1.0)
	var progressBarValue = lerp(0, 100, boostTimerRatio)
	progressBar.value = int(progressBarValue)
