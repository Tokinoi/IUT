extends Area2D

var velocity : Vector2
@onready var sprite = $AnimatedSprite2D
@onready var enemies = []

func _ready():
	sprite.play()

func _on_body_entered(body):
	if body.has_method("damage"):
		body.damage(5)
		queue_free() 

func _on_body_exited(_body):
	pass
