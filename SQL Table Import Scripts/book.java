package db;

import java.io.*;
import java.util.*;

public class book {
	public static void main(String args[]){
		String currentLine, field[];
		field = new String[3];
		try {
			File sqlFile = new File("C:\\Users\\cooltwin\\Desktop\\database\\db project\\sql_book.sql");
			sqlFile.getParentFile().mkdirs();
			PrintWriter writer1 = new PrintWriter(sqlFile);
			File csvFile = new File("C:\\Users\\cooltwin\\Desktop\\database\\db project\\SQL_library_project_data\\books_authors.csv");
			csvFile.getParentFile().mkdirs();
			Scanner scanner = new Scanner(csvFile);
			while(scanner.hasNextLine()) {
				currentLine = scanner.nextLine();
				field = currentLine.split("\t");
				writer1.println("INSERT INTO BOOK VALUES ('"+field[0]+"',\""+field[2]+"\");");
			}
			scanner.close();
			writer1.close();
		}catch(FileNotFoundException e)  {
			System.out.println("file not found"+e);
		}



	}

}




