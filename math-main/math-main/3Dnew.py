import numpy as np
import matplotlib.pyplot as plt
from mpl_toolkits import mplot3d
from matplotlib import cm
from matplotlib import colormaps
from random import *
import math

print("Listes des couleurs disponibles par défault :")
print(list(colormaps))
fig = plt.figure(facecolor="Black")
ax = plt.axes(projection="3d")

def rotz(angle):
    return np.array([[math.cos(angle),-math.sin(angle),0],[math.sin(angle),math.cos(angle),0],[0,0,1]])

ax.set_xlabel('x')
ax.set_ylabel('y')
ax.set_zlabel('z')
ax.set_xlim(-10,10)
ax.set_ylim(-10,10)
ax.set_zlim(-8,8)
u= np.linspace(0,2*np.pi,100)
v= np.linspace(0,np.pi,100)


class Astre():
    def __init__(self,x,y,z,r):
        self.position = (x,y,z)
        self.r = r
        self.x= x+ r * np.outer(np.cos(u),np.sin(v))
        self.y= y+ r * np.outer(np.sin(u),np.sin(v))
        self.z= z+ r * np.outer(np.ones(np.size(u)),np.cos(v))
        self.revolution = (x**3/ (25) )**0.5
        self.moons = []
    def rotate(self):
        self.position = tuple(np.dot(rotz(0.1/(self.revolution/2)),self.position))
    def turn(self):
        revolution = (self.x**3/ (25) )**0.5
        self.x = self.position[0] + self.r * np.outer(np.cos(u+k/10),np.sin(v))
        self.y = self.position[1] + self.r * np.outer(np.sin(u+k/10),np.sin(v))
        self.z = self.position[2] + self.r * np.outer(np.ones(np.size(u+k/10)),np.cos(v))
    def add_moon(self, moon):
        self.moons.append(moon)

class Moon(Astre):
    def __init__(self,x,y,z,r,center):
        self.center = center
        self.position = (x,y,z)
        self.r = r
        self.x= x+ r * np.outer(np.cos(u),np.sin(v))
        self.y= y+ r * np.outer(np.sin(u),np.sin(v))
        self.z= z+ r * np.outer(np.ones(np.size(u)),np.cos(v))
        self.revolution = (x**3/ (25) )**0.5
    def rotate(self):
        rotation_matrix = rotz(0.1 / (self.revolution / 2))
        relative_position = np.array(self.position) - np.array(self.center.position)
        rotated_relative_position = np.dot(rotation_matrix, relative_position)
        self.position = tuple(rotated_relative_position + np.array(self.center.position))
    def turn(self):
        revolution = (self.x**3 / 25)**0.5
        self.x = self.center.position[0] + self.r * np.outer(np.cos(u + k / 10), np.sin(v))
        self.y = self.center.position[1] + self.r * np.outer(np.sin(u + k / 10), np.sin(v))
        self.z = self.center.position[2] + self.r * np.outer(np.ones(np.size(u + k / 10)), np.cos(v))

sun= Astre(0,0,0,1.5)

nb_planet = randint(1,2)


astral = list()
#Le tableau v ci-dessous contient les coordonnées initiales des 3 sommets du triangle
for i in range(nb_planet):
    x =  math.exp(random()*4)
    plan = Astre(x,0,0,math.log(x))
    if random():
        x2 = x/4
        moon = Moon(x2,0,0,2,plan)
        plan.add_moon(moon)
        astral.append(moon)
    astral.append(plan)
print(astral)

etoile=ax.plot_surface(sun.x,sun.y,sun.z, rstride = 10 , cstride = 5, cmap=cm.Oranges ) #en dernier paramètre, couleur de l'étoile

for k in range(10000):
    plot_objects = []
    for astre in astral:
        astre.rotate()
        astre.turn()
        plot_objects.append(ax.plot_surface(astre.x, astre.y, astre.z, rstride=10, cstride=5, cmap=cm.Blues))
        
        if not isinstance(astre, Moon):
            moon_plot_objects = []
            for moon_astre in astre.moons:
                moon_astre.rotate()
                moon_astre.turn()
                plot_objects.append(ax.plot_surface(moon_astre.x, moon_astre.y, moon_astre.z, rstride=10, cstride=5, cmap=cm.Blues))


    plt.pause(0.01)
    for plot_object in plot_objects:
        plot_object.remove()