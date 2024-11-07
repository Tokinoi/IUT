extends VBoxContainer

@onready var SCORELABEL = preload("res://src/UI/score_label.tscn")

func _process(delta):
	# Update leaderboard with tanks score each frame ------
	if get_children():
		var children = get_children()
		for child in children:
			child.queue_free()
	var tanks = get_parent().get_tree().get_nodes_in_group("tank")
	for tank in tanks:
		var scoreLabel = SCORELABEL.instantiate()
		scoreLabel.text = tank.getName() + ': ' + str(tank.score) + '/' + str(tank.deaths)
		add_child(scoreLabel)
	# ------------------------------------
