extends ProgressBar

@onready var player = get_tree().get_nodes_in_group("player")[0]
@onready var LVLText = $"../../LVLText"

func _process(_delta):
	if is_instance_valid(player):
		value = player.CURRENT_EXP
		max_value = player.MAX_EXP
		LVLText.text = "LVL " + str(player.LVL)
