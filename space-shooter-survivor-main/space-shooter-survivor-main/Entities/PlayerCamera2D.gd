extends Camera2D

@onready var player = get_parent()

func _process(_delta):
	offset.x = lerp(offset.x, player.velocity.x/10, 0.02)
	offset.y = lerp(offset.y, player.velocity.y/10, 0.02)
	pass
