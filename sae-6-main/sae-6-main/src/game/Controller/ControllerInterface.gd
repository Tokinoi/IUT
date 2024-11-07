class_name Controller

var tank: Tank


func name(name: String):
	tank.nameLabel.text = "[center]" + name


func exit():
	tank.queue_free()

func col(hexadecimal_color: int):
	tank.self_modulate = Color(hexadecimal_color)

func col_separed(red_value:int, blue_value:int, green_value:int):
	tank.self_modulate = Color(red_value, green_value, blue_value)

func live():
	tank.timeToLive = 60.0

func msg(message: String):
	tank.message = "[center]" + message


func nlist() -> String:
	var tanks = tank.get_all_tanks()
	tanks.map(func(tank: Tank): return tank.nameLabel)
	return "=".join(tanks)


func usrmsg(name: String) -> String:
	for tank: Tank in tank.get_all_tanks():
		if tank.nameLabel == name:
			return tank.message
	return 'EMPTY'


func orient() -> float:
	return tank.rotation


func cbot(pos: Vector2) -> String:
	var enemies = tank.get_all_tanks()
	var closestEnemy = null
	for enemy in enemies:
		if enemy == tank: pass
		if closestEnemy == null: closestEnemy = enemy
		if pos.distance_to(enemy.position) < closestEnemy: closestEnemy = enemy
	if closestEnemy != null:
		return 'rotation=' + closestEnemy.rotation + '/distance=' + pos.distance_to(closestEnemy.global_position)
	return 'EMPTY' # Not in the doc, but seems to be accurate


func nbot(enemy) -> String:
	var tank_enemy: Tank = get_tank_by_name(enemy)
	if tank_enemy == null:
		return 'EMPTY'
	return str(tank_enemy.global_rotation) + "/" + str(tank_enemy.global_position.distance_to(tank.global_position))

func cproj() -> Vector2:
	var pos = tank.global_position
	var bullets = tank.get_tree().get_nodes_in_group("bullet")
	var closestBullet = null
	for bullet in bullets:
		if closestBullet == null: closestBullet = bullet
		if pos.distance_to(bullet.position) < closestBullet: closestBullet = bullet
	return 'rotation=' + closestBullet.rotation + '/distance=' + pos.distance_to(closestBullet.global_position)

func motl(power : float):
	tank.thrusterLeft.power = power - 0.5

func motr(power : float):
	print("motr(" + str(power) + ")")
	tank.thrusterRight.power = power - 0.5


func guntrig(trigger: float):
	tank.gun_component.gun_trig = trigger <= 0.5



func guntrav(orientation: float):
	tank.gun_component.rotation = orientation

func get_tank_by_name(tank_name):
	for tank_scan: Tank in tank.get_all_tanks():
		if tank_scan.nameLabel.text.erase(0, 8) == tank_name:
			return tank_scan
	return null

func process():
	pass
