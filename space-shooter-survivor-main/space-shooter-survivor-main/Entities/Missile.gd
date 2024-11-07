extends Area2D

var velocity : Vector2
var targets
var nearestTarget = self

func _ready():
	velocity.x = sin(rotation) * 2
	velocity.y = -cos(rotation) * 2
	newTarget()
	look_at(nearestTarget.position)

func _process(delta):
	if !is_instance_valid(nearestTarget):
		newTarget()
	else:
		rotation = lerp_angle(rotation, (nearestTarget.global_position - global_position).normalized().angle(), 0.08) 
		velocity.x = cos(rotation) * delta * 400
		velocity.y = sin(rotation) * delta * 400
	translate(velocity)

func newTarget():
	targets = get_tree().get_nodes_in_group("enemies")
	if targets != []:
		nearestTarget = targets[0]
		for target in targets:
			if is_instance_valid(nearestTarget) && is_instance_valid(target):
				if target.global_position.distance_to(global_position) < nearestTarget.global_position.distance_to(global_position):
					nearestTarget = target

func _on_body_entered(body):
	if body.has_method("damage"):
		body.damage(1)
		queue_free()

func _on_timer_timeout():
	queue_free()
