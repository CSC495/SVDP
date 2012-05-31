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

/*
 * Contributors:
 * Henri Chen - henrichen@users.sourceforge.net
 * Kees Kuip  - keeskuip@users.sourceforge.net
 */
package net.sf.jasperreports.ant;

import net.sf.jasperreports.engine.SimpleJasperReportsContext;

import org.apache.tools.ant.taskdefs.MatchingTask;


/**
 * @author Teodor Danciu (teodord@users.sourceforge.net)
 * @version $Id: JRBaseAntTask.java 5179 2012-03-29 13:09:34Z teodord $
 */
public class JRBaseAntTask extends MatchingTask
{

	/**
	 *
	 */
	protected SimpleJasperReportsContext jasperReportsContext = new SimpleJasperReportsContext();
	
}
