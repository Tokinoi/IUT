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

package evoagentsimulation.simulationlearning;

import java.util.ArrayList;
import java.util.Collections;
import java.util.Random;

public class GeneticAlgorithm {
	private int populationSize = 200;
	private int maxGeneration = 100;
	private int repetitions = 1;
	int genomeSize = 0;
	ArrayList<Individual> population;
	
	public class Individual implements Comparable<Individual> {
		double score = 0;
		double genome[]; 

		public Individual(){
			genome = new double[genomeSize];
		}

        public int compareTo(Individual compare) {
            if (this.score <= compare.score){
				return -1;
			}
			if (this.score >= compare.score){
				return 1;
			}
			return 0;
        }

		public void random(){
			for(double weight : genome){
				Random r = new Random();
				
				weight = r.nextDouble();
			}
		}

		public Individual crossbreed(Individual i2){
			Random random = new Random();
			int pivot = random.nextInt(genomeSize);
			double new_genome[] = new double[genomeSize];
			int index =0;
			for(int index1 = 0; index<=pivot; index1++){
			new_genome[index] = genome[index];
			index=index1;
			}
			for(int index2 = 0;index2<=genomeSize-index;index2++){
				new_genome[index] = i2.genome[index];
				index +=1;

			}
			Individual new_indi = new Individual(); 
			new_indi.genome = new_genome;
			return new_indi;

		}

		public void mutation(double probabilite)
		{
			for(double weight : genome){
				Random r = new Random();
				double proba = r.nextDouble();
				if(proba>probabilite){
					if(r.nextDouble() > 0.5){
						weight += r.nextDouble() * 0.1;
					}
					else{
						weight -= r.nextDouble() * 0.1;
					}

				}
			}
		}

		public double[] getGenome() {
			return genome;
		}

		public double getScore() {
			return score;
		}

		public void setScore(double val) {
			score = val;
		}
	}

	public GeneticAlgorithm(int genomeS){
		genomeSize=genomeS;
	}

	public void initialise(){
		population = new ArrayList<Individual>();
		for(int bot_counter=0;bot_counter<=populationSize;bot_counter++){
			Individual indi = new Individual();
			indi.random();
			population.add(indi);
		}
	}	

	public void breedNew(){
		Individual temp_indi; 
		Collections.sort(population);
		for(int couple = 0; couple<=populationSize;couple+=2){
			temp_indi = population.get(couple).crossbreed(population.get(couple+1));
			population.set(couple,population.get(couple).crossbreed(population.get(couple+1)));
			population.set(couple+1,temp_indi);
		}
	}
	
	public Individual getBest(){
		Collections.sort(population);
		return population.get(0);
	}

	public ArrayList<Individual> getPopulation()
	{
		return population;
	}

	public int getMaxGeneration() {
		return maxGeneration;
	}

	public int getPopulationSize() {
		return populationSize;
	}

	public void setPopulationSize(int populationSize) {
		this.populationSize = populationSize;
	}

	public void setMaxGeneration(int maxGeneration) {
		this.maxGeneration = maxGeneration;
	}

	public int getRepetitions() {
		return repetitions;
	}

	public void setRepetitions(int repetitions) {
		this.repetitions = repetitions;
	}
}
