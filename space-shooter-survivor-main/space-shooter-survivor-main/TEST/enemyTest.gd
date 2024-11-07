class_name Enemy extends CharacterBody2D

@export var MAX_SPEED = 110.0
@export var EXP_VALUE = 1
@export var HEALTH = 1

@onready var sprite = $Sprite2D
@onready var shield = $Shield
@onready var animationPlayer = $AnimationPlayer
@onready var blasterPlayer = $BlasterPlayer
@onready var EXPLOSIONPARTICLES = preload("res://Particles/explosionParticles.tscn")
@onready var EXP_BALL = preload("res://Entities/Loot/exp_ball.tscn")
@onready var HEALTH_CROSS = preload("res://Entities/Loot/health_cross.tscn")
@onready var BULLET = preload("res://Entities/bullet.tscn")
@onready var player = get_tree().get_nodes_in_group("player")[0]
@onready var stage = get_parent()
var hasExploded = false

func _ready():
	animationPlayer.play("engine_on")

func _process(delta):
	if HEALTH <= 0:
		velocity = lerp(velocity,Vector2.ZERO,0.01)
		death()
	else:
		look_at(player.position)
		var target_pos = (player.position - position).normalized()
		
		velocity.x = target_pos.x * MAX_SPEED * delta * 50
		velocity.y = target_pos.y * MAX_SPEED * delta * 50
		
		for i in get_slide_collision_count():
			var collision = get_slide_collision(i)
			var collider = collision.get_collider()
			if collider != null:
				if collider.is_in_group("player"):
					collider.damage(1)
					if !shield.visible:
						HEALTH -= 1
	
	velocity = velocity.clamp(Vector2(-100,-100),Vector2(100,100)) # to avoid excess speed after colliding
	move_and_slide()
	
	if randi_range(0,500) == 0:
		shieldUp()
	if randi_range(0,5000) == 0:
		attack()

func attack():
	var bullet = BULLET.instantiate()
	bullet.position.x = position.x
	bullet.position.y = position.y
	bullet.collision_layer = 0x0001
	bullet.collision_mask = 0x0001
	bullet.targetName = "player"
	bullet.SPEED = 300
	bullet.scale *= scale.x/2
	stage.add_child(bullet)
	blasterPlayer.play()
	bullet.rotation_degrees += randi_range(-10,10)
	var offset = Vector2(40,0).rotated(bullet.rotation)
	bullet.position.x += offset.x
	bullet.position.y += offset.y

func shieldUp():
	shield.visible = true
	animationPlayer.play("Shield")

func damage(value: int):
	if !shield.visible:
		HEALTH -= value
		var baseColor = modulate
		sprite.modulate = Color(255,0,0)
		await get_tree().create_timer(0.02).timeout
		sprite.modulate = baseColor

func death():
	animationPlayer.play("explode")
	if !hasExploded:
		hasExploded = true
		$Engine.visible = false
		$Shield.visible = false
		$CollisionShape2D.disabled = true
		explosionParticle()
	await get_tree().create_timer(0.45).timeout
	if randi_range(0,100) == 0:
		var health_cross = HEALTH_CROSS.instantiate()
		health_cross.position = position
		get_parent().call_deferred("add_child", health_cross)
	else:
		var exp_ball = EXP_BALL.instantiate()
		exp_ball.position = position
		exp_ball.VALUE = EXP_VALUE
		get_parent().call_deferred("add_child", exp_ball)
	sprite.self_modulate = Color(255,0,0)
	Globals.enemyKills += 1
	queue_free()

func explosionParticle():
	var explosionParticles = EXPLOSIONPARTICLES.instantiate()
	explosionParticles.scale = Vector2(0.5, 0.5)
	explosionParticles.process_material.scale_min = 0.1
	explosionParticles.process_material.scale_max = 0.3
	explosionParticles.rotation_degrees = rotation_degrees+180
	explosionParticles.position = position
	get_parent().add_child(explosionParticles)


func _on_animation_player_animation_finished(anim_name):
	if anim_name == "Shield":
		shield.visible = false
