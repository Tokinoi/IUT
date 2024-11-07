extends Node2D

var playerControll : StreamPeerTCP
var orientation = 0

# Called when the node enters the scene tree for the first time.
func _ready():
	playerControll.put_string('NAME=Mon nom\n')
# Called every frame. 'delta' is the elapsed time since the previous frame.
func _process(delta):
		var move_vector = $MoveJoystick.output
		var shoot_vector = $ShootJoystick.output

		playerControll.put_string('ORIENT\n')
		var data: int = playerControll.get_available_bytes()

		if data > 0:
			orientation = float(playerControll.get_string(data)) +0.25
			if orientation >= 1:
				orientation-=1
		if move_vector.x != 0 and move_vector.y != 0:
			move_vector = move_vector.normalized()
			var motR = -move_vector.x *0.5 - move_vector.y 
			var motL = move_vector.x *0.5 - move_vector.y 
			playerControll.put_string('MotL='+str(motL)+'\n')
			playerControll.put_string('MotR='+str(motR)+'\n')
		else:
			playerControll.put_string('MotL=0.5\n')
			playerControll.put_string('MotR=0.5\n')
		if shoot_vector.x != 0 and shoot_vector.y != 0:
			playerControll.put_string('GunTrig=1\n')
			var angle_deg = atan2(-shoot_vector.y,shoot_vector.x) * 180/PI
			angle_deg += orientation*360
			if angle_deg < 0:
				angle_deg += 360
			
			var normalized_angle = angle_deg / 360.0
			playerControll.put_string('GunTrav='+str(normalized_angle-0.25)+'\n')
		else:
			playerControll.put_string('GunTrig=0\n')
