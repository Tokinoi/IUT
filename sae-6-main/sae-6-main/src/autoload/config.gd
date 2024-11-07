extends Node

var areNameVisible = true
var isBgVisible = true
var arenaHeight = "648"
var arenaWidth = "1152"
var obstacleProbability = "0.5"
var scoreLimit = "-1"
var timeToLive = "60.0"
var rateOfFire = "1.0"
var stormPos = Vector2(0,0)
var shrinking = false

func spawnArea(offset):
	return Vector2(randi_range(offset, int(Config.arenaWidth)-offset),
		randi_range(offset, int(Config.arenaHeight)-offset))
