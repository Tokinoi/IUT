/*******************************************************************************
 *  * EvoAgentStudent : A simulation platform for agents using neurocontrollers
 *  * Copyright (c)  2020 Suro François (suro@lirmm.fr)
 *  *
 *  * This program is free software: you can redistribute it and/or modify
 *  * it under the terms of the GNU General Public License as published by
 *  * the Free Software Foundation, either version 3 of the License, or
 *  * any later version.
 *  *
 *  * This program is distributed in the hope that it will be useful,
 *  * but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  * GNU General Public License for more details.
 *  *
 *  * You should have received a copy of the GNU General Public License
 *  * along with this program.  If not, see <https://www.gnu.org/licenses/>.
 *******************************************************************************/


package evoagentmindelements;

import java.io.BufferedReader;
import java.io.File;
import java.io.FileInputStream;
import java.io.FileNotFoundException;
import java.io.FileWriter;
import java.io.IOException;
import java.io.InputStream;
import java.io.InputStreamReader;
import java.io.PrintWriter;
import java.util.ArrayList;

import evoagentmindelements.modules.ActuatorModule;
import evoagentmindelements.modules.SensorModule;

public class EvoAgentMind {
	private ArrayList<ActuatorModule> actuatorModules = new ArrayList<>();
	private ArrayList<SensorModule> sensorModules = new ArrayList<>();
	private int hiddenLayerCount = 5;
	private int hiddenLayerSize = 5;
	double weights[];

	public EvoAgentMind()
	{
		
	}
	
	public int genomeSize() {
		int sensor_number = this.sensorModules.size();
		int actuator_number = this.actuatorModules.size();
		return sensor_number * this.hiddenLayerSize +(this.hiddenLayerCount - 1 ) * (this.hiddenLayerSize-1) + actuator_number * this.setHiddenLayerSize;
	}
	
	public void doStep() {
		int weights_index = 0;

		float last_hiddenLayerValues[] = new float[this.hiddenLayerSize];
		float actuator_values[] = new float[this.actuatorModules.size()];
		float hiddenLayerValues[] = new float[this.hiddenLayerSize];
		hiddenLayerValues[0] = 1;

		//TODO WHAT IF SENSOR IS CONNECTED DIRECTLY TO ACTUATOR

		//RETRIVE SENSOR VALUE
		/*
			For each Sensor, get their value
				Ignore the first next node (his value is ALWAYS 1)
				Get next nodes value 
					Next node current value (0) + Sensor.value * weight
					increment weight index

		*/
		//
		for (SensorModule sensor : this.sensorModules) {
            double sensor_input = sensor.getValue();
			
			for(int hiddenLayerIndex = 1; hiddenLayerIndex <= hiddenLayerSize;hiddenLayerIndex++){
				hiddenLayerValues[hiddenLayerIndex] += hiddenLayerValues[hiddenLayerIndex] + sensor_input*this.weights[weights_index];
				weights_index +=1;

			}

        }

		//PROCESS HIDDEN CALCULUS
				/*
			For each Layers, 
				Move current Node value in Last node value and clear the current Node (to prevent accumulation of value from previous calculus)
				For each Node in this layers, get their value
					Ignore the first next node (his value is ALWAYS 1)
					Get next nodes value 
						Next node current value (0) + Sensor.value * weight
						increment weight index

		*/
		//----------------------------------------------
		for(int hiddenLayerIndex = 0; hiddenLayerIndex <= hiddenLayerCount-1;hiddenLayerIndex++){
			last_hiddenLayerValues= hiddenLayerValues ;
			hiddenLayerValues = new float[this.hiddenLayerSize];
			hiddenLayerValues[0] = 1;
			for(float current_weights : hiddenLayerValues){
				for(int size = 1;size<=hiddenLayerSize;size++){
					hiddenLayerValues[hiddenLayerIndex] += last_hiddenLayerValues[hiddenLayerIndex] * this.weights[weights_index];
					weights_index +=1;
				}
			}
		}

		//CALCULATE ACTUATOR VALUES 
		/*
			For each node in the last layers, 
				Update the actuator value (same calculus from before actuator weight + current weight * weight)
		*/
		//----------------------------------------------
		last_hiddenLayerValues =hiddenLayerValues;
		for(float current_weights : hiddenLayerValues){
			for(int size = 0;size<=this.actuatorModules.size();size++){
				actuator_values[size] += current_weights * this.weights[weights_index];
				weights_index +=1;
			}
		}

	//GIVE ACTUATOR CALCULATED VALUE
	for(int actuator_index= 0;actuator_index<=this.actuatorModules.size();actuator_index++){
		this.actuatorModules.get(actuator_index).setMotorValue(normalize(actuator_values[actuator_index]));
	}


		// ...
		//sensorModules.get(0).getValue();
		//actuatorModules.get(0).setMotorValue(0.5);
	}
	
	public double normalize(double v)
	{
		if(v <= 0){return 0;}
		if (v>=1){return 1;}
		return v;
	}
	
	public double applyTransfer(double v)
	{
		
		return Math.tanh(v);
	}
	
	public void setHiddenLayerCount(int n) {
		hiddenLayerCount = n;
	}

	public void setHiddenLayerSize(int n) {
		hiddenLayerSize = n;
	}
	
	public ArrayList<ActuatorModule> getActuatorModules()
	{
		return actuatorModules;
	}
	
	public ArrayList<SensorModule> getSensorModules()
	{
		return sensorModules;
	}
	
	public void addActuator(String actuatorID)
	{
		actuatorModules.add(new ActuatorModule(actuatorID));
	}

	public void addSensor(String sensorID)
	{
		sensorModules.add(new SensorModule(sensorID));
	}
	
	public void setWeights(double w[])
	{
		weights = w;
	}
	
	public void toFile(File f)
	{
		if(f.exists())
			f.delete();
		try {
			f.createNewFile();
			PrintWriter pw = new PrintWriter(new FileWriter(f),true);
			pw.println(hiddenLayerCount + " " 
					+ hiddenLayerSize + " " 
					+ sensorModules.size() + " " 
					+ actuatorModules.size() + " "
					+ genomeSize());
			for(int i = 0 ; i < sensorModules.size();i++)
				pw.println(sensorModules.get(i).getID());
			for(int i = 0 ; i < actuatorModules.size();i++)
				pw.println(actuatorModules.get(i).getID());
			for(int i = 0 ; i < weights.length;i++)
				pw.println(weights[i]);
			pw.close();
		} catch (IOException e) {
			e.printStackTrace();
		}
	}
	
	public void fromFile(File f)
	{
		if(f.exists())
		{
			InputStream ips = null;
			try {
				ips = new FileInputStream(f);
			} catch (FileNotFoundException e) {
				e.printStackTrace();
				System.out.println("file not found : " + f);
				return ;
			} 
			InputStreamReader ipsr=new InputStreamReader(ips);
			BufferedReader br=new BufferedReader(ipsr);
			try {
				String line = br.readLine();
				hiddenLayerCount = Integer.parseInt(line.split(" ")[0]);
				hiddenLayerSize = Integer.parseInt(line.split(" ")[1]);
				int sensorCount = Integer.parseInt(line.split(" ")[2]);
				int actuatorCount = Integer.parseInt(line.split(" ")[3]);
				int weightCount = Integer.parseInt(line.split(" ")[4]);
				weights = new double[weightCount];
				for(int i = 0 ; i < sensorCount; i++)
					addSensor(br.readLine());
				for(int i = 0 ; i < actuatorCount; i++)
					addActuator(br.readLine());
				for(int i = 0 ; i < weightCount; i++)
					weights[i] = Double.parseDouble(br.readLine());
			br.close();
			} catch (IOException e) {
				e.printStackTrace();
			}
		}
	}
	
	public static void main(String[] args) {
		System.out.println("Mind test");
		EvoAgentMind mind = new EvoAgentMind();
		mind.addActuator("A1");
		mind.addActuator("A2");
		mind.addSensor("S1");
		mind.addSensor("S2");
		mind.setHiddenLayerCount(2);
		mind.setHiddenLayerSize(4);
		double weights[] = new double[mind.genomeSize()];
		for(int i = 0 ; i < mind.genomeSize(); i++)
			weights[i] = (Math.random() * 2.0) - 1.0;
		mind.setWeights(weights);
		for(int i = 0 ; i < 10 ; i++)
		{
			System.out.println(i);
			for(SensorModule s : mind.getSensorModules())
				s.setValue(i/10.0);
			mind.doStep();
			for(ActuatorModule a: mind.getActuatorModules())
				System.out.println(a.getMotorValue());		
		}
	}		
}
