extends Control

@onready var panel = $Panel
@onready var kills = $Panel/GridContainer/Kills

func _process(_delta):
	var ratio = int(Globals.viewportRect.size.x/800)
	var panelSize = panel.size * ratio
	scale = Vector2(ratio, ratio)
	position = Vector2((Globals.viewportRect.size.x - panelSize.x)/2, (Globals.viewportRect.size.y - panelSize.y)/2)
	
	kills.text = "Ennemis Tués: " + str(Globals.enemyKills)
