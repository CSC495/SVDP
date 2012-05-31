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
package net.sf.jasperreports.engine.query;


/**
 * SQL "EQUAL" clause function.
 * 
 * @author sanda zaharia (shertage@users.sourceforge.net)
 * @version $Id: JRSqlNotEqualClause.java 5180 2012-03-29 13:23:12Z teodord $
 * @see #apply(JRClauseTokens, JRQueryClauseContext)
 */
public class JRSqlNotEqualClause extends JRSqlAbstractEqualClause
{

	protected static final String OPERATOR_NOT_EQUAL = "<>";
	protected static final String OPERATOR_IS_NOT_NULL = "IS NOT NULL";
	
	protected static final JRSqlNotEqualClause singleton = new JRSqlNotEqualClause();
	
	/**
	 * Returns the singleton function instance.
	 * 
	 * @return the singleton function instance
	 */
	public static JRSqlNotEqualClause instance()
	{
		return singleton;
	}
	
	@Override
	protected void handleEqualOperator(StringBuffer sbuffer, String param, JRQueryClauseContext queryContext)
	{
		Object paramValue = queryContext.getValueParameter(param).getValue();
		if(paramValue == null)
		{
			sbuffer.append(OPERATOR_IS_NOT_NULL);
		}
		else
		{
			sbuffer.append(OPERATOR_NOT_EQUAL);
			finalizeClause(sbuffer, param, queryContext);
		}
	}

}
