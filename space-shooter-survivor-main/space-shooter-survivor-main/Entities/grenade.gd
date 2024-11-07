extends Area2D

@onready var sprite = $Sprite
@onready var iCircle = $InvertedCircle
@onready var collision = $CollisionShape2D
var velocity = Vector2.ZERO
var time_left = 3.0

func _ready():
	$AnimationPlayer.play('idle')
	velocity.x = cos(rotation_degrees * PI / 180) * 5
	velocity.y = sin(rotation_degrees * PI / 180) * 5

func _process(delta):
	time_left -= delta
	translate(velocity)
	velocity = lerp(velocity,Vector2.ZERO,0.01)
	
	if snapped(time_left,0.1) == 3:
		flash()
	if snapped(time_left,0.1) == 2:
		flash()
	if snapped(time_left,0.1) == 1:
		flash()
	if time_left < 0.0:
		collision_layer = 0b00000000_00000000_00000000_00000011
		collision_mask = 0b00000000_00000000_00000000_00000011
		explode()
	if time_left < -0.3:
		queue_free()

func flash():
	var base_color = modulate
	sprite.modulate = Color(255,255,255)
	await get_tree().create_timer(0.2).timeout
	sprite.modulate = base_color

func explode():
	iCircle.scale *= 1.2
	collision.scale *= 1.2

func _on_body_entered(body):
	if body.has_method("damage"):
		body.damage(3)
