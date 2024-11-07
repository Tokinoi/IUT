extends Sprite2D

var time: float

func _process(delta):
	time += delta
	scale = Vector2(15*time, 15*time)
