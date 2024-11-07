extends Sprite2D

var cooldown = 0.5
var baseColor
var dingdong = true

func _ready():
	baseColor = modulate

func _process(delta):
	cooldown -= delta
	if cooldown <= 0.0:
		if dingdong:
			modulate = Color(0)
		else:
			modulate = baseColor
		dingdong = !dingdong
		cooldown = 1.0
