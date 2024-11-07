extends GPUParticles2D

@onready var timer = Timer.new()

func _ready():
	timer.wait_time = 1.2
	timer.one_shot = true
	timer.autostart = true
	add_child(timer)
	timer.start()

func _process(_delta):
	if timer.time_left < 0.8:
		emitting = false
	if timer.is_stopped():
		deletion()

func deletion():
	queue_free()
