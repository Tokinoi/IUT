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
    def rotate(self):
        self.position = tuple(np.dot(rotz(0.1/(self.revolution/2)),self.position))
    def turn(self):
        revolution = (self.x**3/ (25) )**0.5
        self.x = self.position[0] + self.r * np.outer(np.cos(u+k/10),np.sin(v))
        self.y = self.position[1] + self.r * np.outer(np.sin(u+k/10),np.sin(v))
        self.z = self.position[2] + self.r * np.outer(np.ones(np.size(u+k/10)),np.cos(v))



sun= Astre(0,0,0,1.5)

nb_planet = randint(3,8)


astral = list()
#Le tableau v ci-dessous contient les coordonnées initiales des 3 sommets du triangle
for i in range(nb_planet):
    x =  math.exp(random()*3)
    astral.append(Astre(x,0,0,math.log(x)))
print(astral)

etoile=ax.plot_surface(sun.x,sun.y,sun.z, rstride = 10 , cstride = 5, cmap=cm.Oranges ) #en dernier paramètre, couleur de l'étoile

for k in range(10000): 
    plan = list()
    for astre in astral:
        #l'angle 1/80 est lié à la vitesse de révolution autour de l'etoile
        astre.rotate()
        #l'angle 1/10 est lié à la vitesse de rotation de la planète sur elle-même
        astre.turn()
        plan.append(ax.plot_surface(astre.x,astre.y,astre.z, rstride = 10 , cstride = 5 , cmap=cm.Blues )) #en dernier paramètre, couleur de la planete
    plt.pause(0.01)
    for planete in plan:
        planete.remove()