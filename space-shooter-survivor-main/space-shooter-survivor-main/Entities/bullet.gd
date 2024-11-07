extends Area2D

var velocity: Vector2
@onready var sprite = $AnimatedSprite2D
@export var targetName = "enemies"
@export var SPEED = 1000
@export var DURATION = 3
var targets
var nearestTarget = self

func _ready():
	sprite.play()
	newTarget()
	look_at(nearestTarget.position)
	deathByAge()

func newTarget():
	targets = get_tree().get_nodes_in_group(targetName)
	if targets != []:
		nearestTarget = targets[0]
		for target in targets:
			if is_instance_valid(nearestTarget) && is_instance_valid(target):
				if target.global_position.distance_to(global_position) < nearestTarget.global_position.distance_to(global_position):
					nearestTarget = target
		nearestTarget = nearestTarget

func _process(delta):
	if !is_instance_valid(nearestTarget):
		newTarget()
	else:
		velocity.x = cos(rotation_degrees * PI / 180) * delta * SPEED
		velocity.y = sin(rotation_degrees * PI / 180) * delta * SPEED
	translate(velocity)

func _on_body_entered(body):
	if body.has_method("damage"):
		body.damage(1)
	if body is Enemy:
		if body.shield.visible:
			rotation -= 160
		else:
			queue_free()
	else:
		queue_free()

func deathByAge():
	await get_tree().create_timer(DURATION).timeout
	queue_free()
