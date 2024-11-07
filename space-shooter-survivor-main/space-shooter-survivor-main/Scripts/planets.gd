extends StaticBody3D

var a : int = 0

func _process(_delta):
	rotation_degrees.y = a
	a = a + 1
