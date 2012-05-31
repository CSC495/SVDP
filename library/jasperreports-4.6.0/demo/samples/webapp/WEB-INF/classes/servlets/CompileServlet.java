/*
 * JasperReports - Free Java Reporting Library.
 * Copyright (C) 2001 - 2011 Jaspersoft Corporation. All rights reserved.
 * http://www.jaspersoft.com
 *
 * Unless you have purchased a commercial license agreement from Jaspersoft,
 * the following license terms apply:
 *
 * This program is part of JasperReports.
 *
 * JasperReports is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * JasperReports is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with JasperReports. If not, see <http://www.gnu.org/licenses/>.
 */
package servlets;

import java.io.IOException;
import java.io.PrintWriter;

import javax.servlet.ServletContext;
import javax.servlet.ServletException;
import javax.servlet.http.HttpServlet;
import javax.servlet.http.HttpServletRequest;
import javax.servlet.http.HttpServletResponse;

import net.sf.jasperreports.engine.JRException;
import net.sf.jasperreports.engine.JasperCompileManager;


/**
 * @author Teodor Danciu (teodord@users.sourceforge.net)
 * @version $Id: CompileServlet.java 5180 2012-03-29 13:23:12Z teodord $
 */
public class CompileServlet extends HttpServlet
{


	/**
	 *
	 */
	public void service(
		HttpServletRequest request,
		HttpServletResponse response
		) throws IOException, ServletException
	{
		ServletContext context = this.getServletConfig().getServletContext();

		response.setContentType("text/html");
		PrintWriter out = response.getWriter();

		try
		{
			JasperCompileManager.compileReportToFile(context.getRealPath("/reports/WebappReport.jrxml"));
		}
		catch (JRException e)
		{
			out.println("<html>");
			out.println("<head>");
			out.println("<title>JasperReports - Web Application Sample</title>");
			out.println("<link rel=\"stylesheet\" type=\"text/css\" href=\"../stylesheet.css\" title=\"Style\">");
			out.println("</head>");
			
			out.println("<body bgcolor=\"white\">");

			out.println("<span class=\"bnew\">JasperReports encountered this error :</span>");
			out.println("<pre>");

			e.printStackTrace(out);

			out.println("</pre>");

			out.println("</body>");
			out.println("</html>");

			return;
		}

		out.println("<html>");
		out.println("<head>");
		out.println("<title>JasperReports - Web Application Sample</title>");
		out.println("<link rel=\"stylesheet\" type=\"text/css\" href=\"../stylesheet.css\" title=\"Style\">");
		out.println("</head>");
		
		out.println("<body bgcolor=\"white\">");

		out.println("<span class=\"bold\">The JRXML report design was successfully compiled.</span>");

		out.println("</body>");
		out.println("</html>");
	}


}
