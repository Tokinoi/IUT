extends CharacterBody2D

@export var MAX_ROTATION_SPEED = 2
@export var MAX_SPEED = 100.0
@export var BOOST = 2
@export var MAXHEALTH = 10
@export var ATTACK_COOLDOWN = 1.7
@export var SCALE_EXP = 1.5

var HEALTH
var invicibility_frame = 0.3
var shake_amount = 15
var CURRENT_ATTACK_COOLDOWN
var BASE_ATTACK_COOLDOWN
var CURRENT_EXP = 0
var MAX_EXP = 10
var LVL = 1

var boost_timer = 5.0

@onready var spriteBase = $ASBase
@onready var collisionPolygon2D = $CollisionPolygon2D
@onready var animationPlayer = $AnimationPlayer

#WEAPONS
@onready var BULLET = preload("res://Entities/bullet.tscn")
@onready var MISSILE = preload("res://Entities/missile.tscn")
@onready var LIGHTNING = preload("res://Entities/lightning.tscn")
@onready var GRENADE = preload("res://Entities/grenade.tscn")
@onready var blasterPlayer = $BlasterPlayer
@onready var minePlayer = $MinePlayer

#SHIP STATES
@onready var DEATHPARTICLES = preload("res://Particles/deathParticles_2d.tscn")
@onready var WCOD = preload("res://GUI/whiteCircleOfDeath.tscn")
@onready var stage = owner

var fullscreen = true

func _ready():
	HEALTH = MAXHEALTH
	Globals.health = HEALTH
	Globals.boost_timer = boost_timer
	Globals.player = self
	CURRENT_ATTACK_COOLDOWN = ATTACK_COOLDOWN
	BASE_ATTACK_COOLDOWN = ATTACK_COOLDOWN

func _process(delta):
	if HEALTH > MAXHEALTH:
		HEALTH = MAXHEALTH
	if HEALTH > 0:
		if CURRENT_EXP >= MAX_EXP:
			CURRENT_EXP -= MAX_EXP
			MAX_EXP = MAX_EXP * SCALE_EXP
			LVL += 1
		if BASE_ATTACK_COOLDOWN > 0:
			BASE_ATTACK_COOLDOWN -= (delta + lerp(LVL, 0, 0.99))
		else:
			BASE_ATTACK_COOLDOWN = ATTACK_COOLDOWN + CURRENT_ATTACK_COOLDOWN
			baseAttack()
		if CURRENT_ATTACK_COOLDOWN > 0:
			CURRENT_ATTACK_COOLDOWN -= delta
		else:
			CURRENT_ATTACK_COOLDOWN = ATTACK_COOLDOWN + CURRENT_ATTACK_COOLDOWN
			attack()
		
		if Input.is_action_pressed("boost"):
			boost_timer -= delta
			boost_timer = max(0, boost_timer)
		else:
			boost_timer += delta / 2
		
		if Input.is_action_pressed("ui_left"):
			rotation = lerp_angle(rotation, 3*PI/2, 0.1)
			forward(delta)
		elif Input.is_action_pressed("ui_right"):
			rotation = lerp_angle(rotation, PI/2, 0.1)
			forward(delta)
		if Input.is_action_pressed("ui_up"):
			rotation = lerp_angle(rotation, 2*PI, 0.1)
			forward(delta)
		elif Input.is_action_pressed("ui_down"):
			rotation = lerp_angle(rotation, PI, 0.1)
			forward(delta)
		
		if Input.is_action_pressed("left"):
			rotation = lerp_angle(deg_to_rad(rotation_degrees),
			deg_to_rad(rotation_degrees-20), 0.1)
		elif Input.is_action_pressed("right"):
			rotation = lerp_angle(deg_to_rad(rotation_degrees),
			deg_to_rad(rotation_degrees+20), 0.1)
		
		velocity = lerp(velocity,Vector2.ZERO,0.01)
		if invicibility_frame > 0:
			invicibility_frame -= delta
		else:
			invicibility_frame = 0
		
		if HEALTH >= MAXHEALTH:
			spriteBase.play("full")
		elif HEALTH >= MAXHEALTH/1.3:
			spriteBase.play("high")
		elif HEALTH >= MAXHEALTH/1.7:
			spriteBase.play("mid")
		else:
			spriteBase.play("low")
		
	if HEALTH <= 0 && HEALTH > -1000:
		velocity = Vector2.ZERO
		var deathParticles = DEATHPARTICLES.instantiate()
		deathParticles.position = position
		deathParticles.one_shot = true
		stage.add_child(deathParticles)
		visible = false
		collisionPolygon2D.queue_free()
		var wcod = WCOD.instantiate()
		wcod.global_position = global_position
		owner.add_child(wcod)
		owner.levelEnded()
		HEALTH = -9999 #TODO ACTUAL DEATH CONTROLLER

	Globals.health = HEALTH
	Globals.boost_timer = boost_timer

func _physics_process(_delta):
	move_and_slide()

func damage(value: int):
	if invicibility_frame == 0 && value > 0:
		invicibility_frame = 0.3
		HEALTH -= value
		var base_color = modulate
		modulate = Color(255,255,255)
		await get_tree().create_timer(0.2).timeout
		modulate = base_color

func attack():
	if LVL > 1:
		var missile = MISSILE.instantiate()
		missile.position = position
		missile.rotation = rotation
		stage.add_child(missile)
	if LVL > 2:
		var offset = Vector2(0,-50).rotated(rotation)
		var lightning = LIGHTNING.instantiate()
		lightning.position.x = position.x + offset.x
		lightning.position.y = position.y + offset.y
		lightning.rotation = rotation
		stage.add_child(lightning)
		minePlayer.play()
	if LVL > 3:
		var grenade = GRENADE.instantiate()
		grenade.position = position
		grenade.rotation = rotation
		stage.add_child(grenade)

func baseAttack():
	var bullet = BULLET.instantiate()
	bullet.position.x = position.x
	bullet.position.y = position.y
	bullet.modulate = Color(0.0,0.8,0.6)
	stage.add_child(bullet)
	blasterPlayer.play()
	var offset = Vector2(50,0).rotated(bullet.rotation)
	bullet.position.x += offset.x
	bullet.position.y += offset.y

func forward(delta):
	if Input.is_action_pressed("boost") and boost_timer > 0.0:
		animationPlayer.play("boost")
		velocity.x = sin(rotation) * MAX_SPEED * delta * 100 * BOOST
		velocity.y = -cos(rotation) * MAX_SPEED * delta * 100 * BOOST
	else:
		animationPlayer.play("fly")
		velocity.x = sin(rotation) * MAX_SPEED * delta * 100
		velocity.y = -cos(rotation) * MAX_SPEED * delta * 100
