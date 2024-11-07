extends StaticBody2D

@onready var collision2D = $Collision2D
@onready var polygon2D = $Polygon2D

func _ready():
	var width = int(Config.arenaWidth)
	var height = int(Config.arenaHeight)
	collision2D.polygon[6].y = float(height)
	collision2D.polygon[7].y = float(height)
	collision2D.polygon[7].x = float(width)
	collision2D.polygon[8].x = float(width)
	polygon2D.polygon[2].y = float(height)
	polygon2D.polygon[3].y = float(height)
	polygon2D.polygon[1].x = float(width)
	polygon2D.polygon[2].x = float(width)
