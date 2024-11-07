class_name Tank extends RigidBody2D

var hasProcessStarted = false

const MAX_HEALTH = 3
var health = MAX_HEALTH
var score = 0
var deaths = 0
var timeToLive = float(Config.timeToLive)
var isRandom = false
var randomCooldown: float
var currentRandomThrusts: Vector2
var deathCooldown = 3.0

var TANKTYPE
var tankType
var CONTROLLER
var controller
var thrusterRight
var thrusterLeft
var gun_component


@onready var message = $MessageLabel
@onready var nameLabel = $NameLabel

func _init(controller_param: Controller = Controller.new()):
	controller = controller_param
	controller.tank = self

func _ready():
	thrusterLeft = $ThrusterLeft
	thrusterRight = $ThrusterRight
	gun_component = $GunComponent

func _process(delta):
	if isRandom:
		if randomCooldown < 0.0:
			randomCooldown = 3.0
			currentRandomThrusts = Vector2(randf_range(0.8,1.0), randf_range(0.8,1.0))
		else:
			thrusterLeft.power = currentRandomThrusts.x
			thrusterRight.power = currentRandomThrusts.y
			randomCooldown -= delta

	controller.process()
	nameLabel.visible = Config.areNameVisible
	
	timeToLive -= delta
	if timeToLive <= 0.0:
		queue_free()
	if health <= 0:
		queue_free()
		global_position = Vector2(randi_range(-10000, -1000), randi_range(-10000, -1000))
		deathCooldown -= delta
		if deathCooldown <= 0:
			deaths += 1
			health = MAX_HEALTH
			global_position = Vector2(Config.stormPos.x+randi_range(-100, 100), Config.stormPos.y+randi_range(-100, 100))

	hasProcessStarted = true

func _physics_process(delta):
	linear_velocity = linear_velocity.lerp(Vector2.ZERO, 0.1)

func hit(hitOwner=null):
	health -= 1
	if hitOwner && is_instance_valid(hitOwner):
		if health <= 0:
			hitOwner.score += 1
		if hitOwner.score == int(Config.scoreLimit):
			hitOwner.score = int(Config.scoreLimit)
	var base_modulate = self_modulate
	modulate = Color(1)
	await get_tree().create_timer(0.05).timeout
	modulate = base_modulate
	await get_tree().create_timer(0.05).timeout
	modulate = Color(1)
	await get_tree().create_timer(0.05).timeout
	modulate = base_modulate


func infos():
	return 'name=' + str(getName()) + \
	'#position=' + str(global_position) + \
	'#velocity=' + str(linear_velocity) + \
	'#health=' + str(health)

func getName():
	return nameLabel.text.get_slice('[center]', 1)

func set_controller(new_controller: Controller):
	controller = new_controller
	controller.tank = self

func get_all_tanks() -> Array[Node]:
	return get_tree().get_nodes_in_group("tank")
	
func stream():
	return str(self.rotation_degrees) +'!'+ str(self.position)
