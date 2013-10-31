//
//  PostListViewController.m
//  ipapp
//
//  Created by kelaocai on 13-9-29.
//  Copyright (c) 2013年 tongshibang. All rights reserved.
//

#import "ThreadListViewController.h"
#import "common.h"
#import "PostListViewController.h"


@interface ThreadListViewController ()

@end

@implementation ThreadListViewController



- (id)initWithStyle:(UITableViewStyle)style
{
    self = [super initWithStyle:style];
    if (self) {
        // Custom initialization
    }
    return self;
}

- (void)viewDidLoad
{
    [super viewDidLoad];
    _current_page=1;
    _next_page=1;
    _page_size=1;
    _threads=[[NSMutableArray alloc] initWithCapacity:10];
    
    //进度条
    self.hud=[[[MBProgressHUD alloc] initWithView:self.tableView] autorelease];
    [self.view addSubview:self.hud];
    [self.view bringSubviewToFront:self.hud];
    self.hud.delegate=self;
    self.hud.labelText=@"数据加载中...";
    
    [self loadMore];
    
}

- (void)didReceiveMemoryWarning
{
    [super didReceiveMemoryWarning];
    // Dispose of any resources that can be recreated.
}




// 加载数据
-(void)loadMore
{
    
    //启动等待指示器
    [_hud show:YES];
    
    NSMutableArray *more=[[[NSMutableArray alloc] initWithCapacity:0] autorelease];
    
    //读取远程板块数据
    NSString *BaseURLString=BASE_URL;
    NSString *url = [NSString stringWithFormat:@"%@?c=forum&a=forum_thread_list&fid=%@&page=%d", BaseURLString,_fid,_next_page];
    
    //把远程数据转成NSDictionary的数据
    NSDictionary *data = [NSDictionary dictionaryWithContentsOfURLString:url];
    
    if (nil!=data) {
        //关闭等待指示器
        [self.hud hide:YES];
        
        NSArray *threads_data=[data objectForKey:@"data"];
        if ([threads_data count]>0){
            for (int i=0; i<[threads_data count]; i++) {
                [more addObject:[threads_data objectAtIndex:i]];
            }
        }
        
        //更新分页数据
        NSDictionary *mypager=[data objectForKey:@"pager"];
        if ((NSNull *) mypager!=[NSNull null]) {
            _next_page=[[mypager objectForKey:@"next_page"] intValue];
            _current_page=[[mypager objectForKey:@"current_page"] intValue];
            _total_page=[[mypager objectForKey:@"total_page"] intValue];
            _page_size=[[mypager objectForKey:@"page_size"] intValue];
        }else{
            _next_page=1;
            _current_page=1;
            _total_page=1;
            _page_size=1;
        }

        
        
        [self appendTableWith:more];
        
    }else{
        //关闭等待指示器
        [self.hud hide:YES];
        NSLog(@"获取信息失败");
    }
    
    
    
}

// 添加数据到当前TableView中去
-(void) appendTableWith:(NSMutableArray *)data
{
    // 添加到当前的数据源中
    for (int i=0; i<[data count]; i++) {
        [_threads addObject:[data objectAtIndex:i]];
    }
    //[self.tableView reloadData];
    NSMutableArray *insertIndexPaths = [NSMutableArray arrayWithCapacity:[data count]];
    for(int ind =0;ind<[data count];ind++)
    {
        NSIndexPath *newPath = [NSIndexPath indexPathForRow:[_threads indexOfObject:[data objectAtIndex:ind]] inSection:0];
        [insertIndexPaths addObject:newPath];
    }
    [self.tableView insertRowsAtIndexPaths:insertIndexPaths withRowAnimation:UITableViewRowAnimationBottom];
    [self.tableView reloadData];
    
    // 选到当前的行数
    int begin = [_threads count];//(_current_page-1)*_page_size;
    //NSLog(@"bging:%d",begin);
    NSIndexPath *indexPath = [NSIndexPath indexPathForRow:begin inSection:0];
    [self.tableView selectRowAtIndexPath:indexPath  animated:YES scrollPosition:UITableViewScrollPositionTop];
    
}




#pragma mark - Table view data source

- (NSInteger)numberOfSectionsInTableView:(UITableView *)tableView
{
    
    // Return the number of sections.
    return 1;
}

- (NSInteger)tableView:(UITableView *)tableView numberOfRowsInSection:(NSInteger)section
{
    
    return [_threads count]+1;
    
}

- (UITableViewCell *)tableView:(UITableView *)tableView cellForRowAtIndexPath:(NSIndexPath *)indexPath
{
    
    if([indexPath row] == ([_threads count])) {
        
        static NSString *idd=@"cell";
        UITableViewCell *cell = [tableView dequeueReusableCellWithIdentifier:idd];
        if (cell==nil) {
            cell=[[[UITableViewCell alloc] initWithStyle:UITableViewCellStyleDefault reuseIdentifier:idd] autorelease];
        }else{
            //NSLog(@"from deque%d",[indexPath row]);
        }
        
        
        if (_current_page==_total_page) {
            cell.textLabel.text=@"已到最后一页";
        }else{
            cell.textLabel.text=@"加载更多..";
        }
        
        return cell;
        
    }else{
        
        NSString *idd=[NSString stringWithFormat:@"cell%d",indexPath.row];
        ThreadListTableCell *cell = [tableView dequeueReusableCellWithIdentifier:idd];
        if (cell == nil) {
            cell = [[[ThreadListTableCell alloc] initWithStyle:UITableViewCellStyleSubtitle reuseIdentifier:idd] autorelease];
            cell.selectionStyle = UITableViewCellSelectionStyleNone;
        }
        NSDictionary *thread=[self.threads objectAtIndex:[indexPath row]];
        cell.title.text=[thread objectForKey:@"subject"];
        cell.author.text=[thread objectForKey:@"author"];
        NSDictionary *last_reply=[thread objectForKey:@"last_reply"];
        //设置头像
        NSURL *avatar_url = [NSURL URLWithString:[thread objectForKey:@"avatar"]];
        [cell.logo setImageWithURL:avatar_url];
        cell.time.text=[thread objectForKey:@"date"];
        cell.comment.text=[thread objectForKey:@"replies"];
        //最后回复者
        [cell.lastReplyAvtar setImageWithURL:[last_reply objectForKey:@"avatar"]];
        //最后回复概要
        if(nil==[last_reply objectForKey:@"message"]){
            cell.lastPostMessage.text=[NSString stringWithFormat:@"抢沙发..."];
        }else{
            cell.lastPostMessage.text=[NSString stringWithFormat:@"回复:%@", [last_reply objectForKey:@"message"]];
        }
        
        
        return cell;
        
        
    }
    
    
}


- (void)tableView:(UITableView *)tableView didSelectRowAtIndexPath:(NSIndexPath *)indexPath
{
    
    
    if([indexPath row] == [_threads count])
    {
        
        if (_current_page<_total_page) {
            [self loadMore];
        }
        
        [tableView deselectRowAtIndexPath:indexPath animated:YES];
    }else{
        PostListViewController *postListView=[[PostListViewController alloc] init];
        NSDictionary *thread=[self.threads objectAtIndex:[indexPath row]];
        postListView.tid=[thread objectForKey:@"tid"];
        NSLog(@"tid:%@",[thread objectForKey:@"tid"]);
        [self.navigationController pushViewController:postListView animated:YES];
        [postListView release];
    }
    
    
    
}


/*
 // Override to support conditional editing of the table view.
 - (BOOL)tableView:(UITableView *)tableView canEditRowAtIndexPath:(NSIndexPath *)indexPath
 {
 // Return NO if you do not want the specified item to be editable.
 return YES;
 }
 */

/*
 // Override to support editing the table view.
 - (void)tableView:(UITableView *)tableView commitEditingStyle:(UITableViewCellEditingStyle)editingStyle forRowAtIndexPath:(NSIndexPath *)indexPath
 {
 if (editingStyle == UITableViewCellEditingStyleDelete) {
 // Delete the row from the data source
 [tableView deleteRowsAtIndexPaths:@[indexPath] withRowAnimation:UITableViewRowAnimationFade];
 }
 else if (editingStyle == UITableViewCellEditingStyleInsert) {
 // Create a new instance of the appropriate class, insert it into the array, and add a new row to the table view
 }
 }
 */

/*
 // Override to support rearranging the table view.
 - (void)tableView:(UITableView *)tableView moveRowAtIndexPath:(NSIndexPath *)fromIndexPath toIndexPath:(NSIndexPath *)toIndexPath
 {
 }
 */

/*
 // Override to support conditional rearranging of the table view.
 - (BOOL)tableView:(UITableView *)tableView canMoveRowAtIndexPath:(NSIndexPath *)indexPath
 {
 // Return NO if you do not want the item to be re-orderable.
 return YES;
 }
 */

/*
 #pragma mark - Table view delegate
 
 // In a xib-based application, navigation from a table can be handled in -tableView:didSelectRowAtIndexPath:
 - (void)tableView:(UITableView *)tableView didSelectRowAtIndexPath:(NSIndexPath *)indexPath
 {
 // Navigation logic may go here, for example:
 // Create the next view controller.
 <#DetailViewController#> *detailViewController = [[<#DetailViewController#> alloc] initWithNibName:@"<#Nib name#>" bundle:nil];
 
 // Pass the selected object to the new view controller.
 
 // Push the view controller.
 [self.navigationController pushViewController:detailViewController animated:YES];
 }
 
 */

- (void)dealloc {
    [_threadTableView release];
    [_threads release];
    [super dealloc];
}
@end
